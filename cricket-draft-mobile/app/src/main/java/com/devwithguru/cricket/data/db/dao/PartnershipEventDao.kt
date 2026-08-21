package com.devwithguru.cricket.data.db.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.devwithguru.cricket.data.db.entity.PartnershipEventEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface PartnershipEventDao {
    @Query("SELECT * FROM partnership_events WHERE inningsId = :inningsId ORDER BY wicketNumber")
    fun getPartnershipsByInnings(inningsId: Long): Flow<List<PartnershipEventEntity>>

    @Query("SELECT * FROM partnership_events WHERE inningsId = :inningsId ORDER BY wicketNumber")
    suspend fun getPartnershipsByInningsSync(inningsId: Long): List<PartnershipEventEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertPartnership(partnership: PartnershipEventEntity): Long

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(partnerships: List<PartnershipEventEntity>)

    @Query("DELETE FROM partnership_events WHERE inningsId = :inningsId")
    suspend fun deleteAllByInnings(inningsId: Long)
}
